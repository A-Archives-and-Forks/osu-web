###
#    Copyright 2015-2017 ppy Pty. Ltd.
#
#    This file is part of osu!web. osu!web is distributed with the hope of
#    attracting more community contributions to the core ecosystem of osu!.
#
#    osu!web is free software: you can redistribute it and/or modify
#    it under the terms of the Affero GNU General Public License version 3
#    as published by the Free Software Foundation.
#
#    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
#    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
#    See the GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
###

{div, span, table, tbody, td, th, tr} = React.DOM
el = React.createElement

class BeatmapsetPage.Stats extends React.Component
  componentDidMount: ->
    @_renderChart()

  componentWillUnmount: =>
    $(window).off '.beatmapsetPageStats'

  componentDidUpdate: ->
    @_renderChart()

  _renderChart: ->
    data = [
      {values: @props.beatmapset.ratings[1..]}
    ]

    unless @_ratingChart
      options =
        scales:
          x: d3.scale.linear()
          y: d3.scale.linear()
        modifiers: ['beatmapset-rating']

      @_ratingChart = new StackedBarChart @refs.chartArea, options
      $(window).on 'throttled-resize.beatmapsetPageStats', @_ratingChart.resize

    @_ratingChart.loadData data

  togglePreview: (e) =>
    $.publish 'beatmapset:preview:toggle', !@props.isPreviewPlaying

  render: ->
    audioPreview = document.getElementsByClassName('js-beatmapset-page--audio-preview')[0]

    ratingsPositive = 0
    ratingsNegative = 0

    for rating, count of @props.beatmapset.ratings
      ratingsNegative += count if rating >= 1 && rating <= 5
      ratingsPositive += count if rating >= 6 && rating <= 10

    ratingsAll = ratingsPositive + ratingsNegative

    div className: 'beatmapset-stats',
      div
        className: "beatmapset-stats__row beatmapsets-stats__row beatmapset-stats__row--preview"
        onClick: @togglePreview
        div
          className: 'beatmapset-stats__preview-icon'
          el Icon, name: if @props.isPreviewPlaying then 'stop' else 'play'

        div
          className: 'beatmapset-stats__elapsed-bar'
          style: if @props.isPreviewPlaying
            transitionDuration: "#{audioPreview.duration}s"
            width: '100%'
          else
            transitionDuration: '0s'
            width: '0%'

      div className: 'beatmapset-stats__row beatmapset-stats__row--basic',
        el BeatmapBasicStats,
          beatmapset: @props.beatmapset
          beatmap: @props.beatmap

      div className: 'beatmapset-stats__row beatmapset-stats__row--advanced',
        table className: 'beatmap-stats-table',
          tbody null,
            for stat in ['cs', 'drain', 'accuracy', 'ar', 'stars']
              value =
                if stat == 'stars'
                  @props.beatmap.difficulty_rating
                else
                  @props.beatmap[stat]

              valueText =
                if stat == 'stars'
                  value.toFixed 2
                else
                  value.toLocaleString()

              if @props.beatmap.mode == 'mania' && stat == 'cs'
                stat += '-mania'

              tr
                key: stat
                th className: 'beatmap-stats-table__label', osu.trans "beatmaps.beatmapset.show.stats.#{stat}"
                td className: 'beatmap-stats-table__bar',
                  div className: "bar bar--beatmap-stats bar--beatmap-stats-#{stat}",
                    div
                      className: 'bar__fill'
                      style:
                        width: "#{10 * Math.min 10, value}%"
                td className: 'beatmap-stats-table__value', valueText

      div className: 'beatmapset-stats__row beatmapset-stats__row--rating',
        div className: 'beatmapset-stats__rating-header', osu.trans 'beatmaps.beatmapset.show.stats.user-rating'
        div className: 'bar--beatmap-rating',
          div
            className: 'bar__fill'
            style:
              width: "#{(ratingsNegative / ratingsAll) * 100}%"

        div className: 'beatmapset-stats__rating-values',
          span null, ratingsNegative.toLocaleString()
          span null, ratingsPositive.toLocaleString()

        div className: 'beatmapset-stats__rating-header', osu.trans 'beatmaps.beatmapset.show.stats.rating-spread'

        div
          className: 'beatmapset-stats__rating-chart'
          ref: 'chartArea'
