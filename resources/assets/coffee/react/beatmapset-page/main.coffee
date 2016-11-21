###
# Copyright 2015-2016 ppy Pty. Ltd.
#
# This file is part of osu!web. osu!web is distributed with the hope of
# attracting more community contributions to the core ecosystem of osu!.
#
# osu!web is free software: you can redistribute it and/or modify
# it under the terms of the Affero GNU General Public License version 3
# as published by the Free Software Foundation.
#
# osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
# warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
# See the GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
###
{div, audio} = React.DOM
el = React.createElement

class BeatmapsetPage.Main extends React.Component
  constructor: (props) ->
    super props

    optionsHash = BeatmapsetPageHash.parse location.hash
    @initialPage = optionsHash.page

    beatmaps = _.concat props.beatmapset.beatmaps, props.beatmapset.converts
    beatmaps = BeatmapHelper.group beatmaps

    currentBeatmap = BeatmapHelper.find
      group: beatmaps
      id: optionsHash.beatmapId
      mode: optionsHash.playmode

    # fall back to the first mode that has beatmaps in this mapset
    currentBeatmap ?= BeatmapHelper.default items: beatmaps[optionsHash.playmode]
    currentBeatmap ?= BeatmapHelper.default group: beatmaps

    @state =
      beatmaps: beatmaps
      currentBeatmap: currentBeatmap
      loading: false
      isPreviewPlaying: false
      currentScoreboardType: 'global'
      enabledMods: []
      scores: []
      userScore: null
      userScorePosition: -1


  setHash: =>
    osu.setHash BeatmapsetPageHash.generate
      beatmap: @state.currentBeatmap


  setCurrentScoreboard: (_e, {
    scoreboardType = @state.currentScoreboardType,
    enabledMod = null,
    forceReload = false,
    resetMods = false
  }) =>
    @xhr?.abort()

    @setState
      currentScoreboardType: scoreboardType

    if scoreboardType != 'global' && !currentUser.isSupporter
      @setState scores: []
      return

    enabledMods = if resetMods
      []
    else if enabledMod != null && _.includes @state.enabledMods, enabledMod
      _.without @state.enabledMods, enabledMod
    else if enabledMod != null
      _.concat @state.enabledMods, enabledMod
    else
      @state.enabledMods

    @scoresCache ?= {}
    cacheKey = "#{@state.currentBeatmap.id}-#{@state.currentBeatmap.mode}-#{_.sortBy enabledMods}-#{scoreboardType}"

    loadScore = =>
      @setState
        scores: @scoresCache[cacheKey].scoresList
        userScore: @scoresCache[cacheKey].userScore if @scoresCache[cacheKey].userScore?
        userScorePosition: @scoresCache[cacheKey].userScorePosition
        enabledMods: enabledMods

    if !forceReload && @scoresCache[cacheKey]?
      loadScore()
      return

    $.publish 'beatmapset:scoreboard:loading', true
    @setState loading: true

    @xhr = $.ajax (laroute.route 'beatmaps.scores', beatmap: @state.currentBeatmap.id),
      method: 'GET'
      dataType: 'JSON'
      data:
        type: scoreboardType
        enabledMods: enabledMods
        mode: @state.currentBeatmap.mode

    .done (data) =>
      @scoresCache[cacheKey] = data
      loadScore()

    .fail (xhr, status) =>
      if status == 'abort'
        return

      osu.ajaxError xhr

    .always =>
      $.publish 'beatmapset:scoreboard:loading', false
      @setState loading: false


  setCurrentBeatmap: (_e, {beatmap}) =>
    return unless beatmap?
    return if @state.currentBeatmap.id == beatmap.id && @state.currentBeatmap.mode == beatmap.mode

    @setState
      currentBeatmap: beatmap
      =>
        @setHash()
        @setCurrentScoreboard null, scoreboardType: 'global', resetMods: true


  setCurrentPlaymode: (_e, {mode}) =>
    return if @state.currentBeatmap.mode == mode

    @setCurrentBeatmap null,
      beatmap: BeatmapHelper.default items: @state.beatmaps[mode]


  togglePreviewPlayingState: (_e, isPreviewPlaying) =>
    @setState isPreviewPlaying: isPreviewPlaying

    if isPreviewPlaying
      @audioPreview.play()
    else
      @audioPreview.pause()
      @audioPreview.currentTime = 0


  setHoveredBeatmap: (_e, hoveredBeatmap) =>
    @setState hoveredBeatmap: hoveredBeatmap


  onPreviewEnded: =>
    @setState isPreviewPlaying: false


  componentDidMount: ->
    $.subscribe 'beatmapset:beatmap:set.beatmapsetPage', @setCurrentBeatmap
    $.subscribe 'beatmapset:mode:set.beatmapsetPage', @setCurrentPlaymode
    $.subscribe 'beatmapset:scoreboard:set.beatmapsetPage', @setCurrentScoreboard
    $.subscribe 'beatmapset:preview:toggle.beatmapsetPage', @togglePreviewPlayingState
    $.subscribe 'beatmapset:hoveredbeatmap:set.beatmapsetPage', @setHoveredBeatmap

    @setHash()
    @setCurrentScoreboard null, scoreboardType: 'global', resetMods: true

    @audioPreview = document.getElementsByClassName('js-beatmapset-page--audio-preview')[0]
    @audioPreview.volume = 0.45


  componentWillUnmount: ->
    $.unsubscribe '.beatmapsetPage'
    @xhr?.abort()


  render: ->
    div className: 'osu-layout__section',
      audio
        className: 'js-beatmapset-page--audio-preview'
        src: @props.beatmapset.previewUrl
        preload: 'auto'
        onEnded: @onPreviewEnded

      div className: 'osu-layout__row osu-layout__row--page-compact',
        el BeatmapsetPage.Header,
          beatmapset: @props.beatmapset
          beatmaps: @state.beatmaps
          currentBeatmap: @state.currentBeatmap
          hoveredBeatmap: @state.hoveredBeatmap
          isPreviewPlaying: @state.isPreviewPlaying

        el BeatmapsetPage.Info,
          beatmapset: @props.beatmapset
          beatmap: @state.currentBeatmap

      div className: 'osu-layout__section osu-layout__section--extra',
        el BeatmapsetPage.Scoreboard,
          type: @state.currentScoreboardType
          beatmap: @state.currentBeatmap
          scores: @state.scores
          userScore: @state.userScore
          userScorePosition: @state.userScorePosition
          enabledMods: @state.enabledMods
          countries: @props.countries
          loading: @state.loading
