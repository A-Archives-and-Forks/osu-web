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

el = React.createElement


class @FlagCountry extends React.Component
  render: =>
    return el 'span' unless @props.country.code

    baseClass = 'flag-country'
    additionalClasses = (@props.classModifiers || [])
      .map (m) ->
        "#{baseClass}--#{m}"
      .join " "

    el 'span',
      className: "#{baseClass} #{additionalClasses}"
      title: @props.country.name
      style:
        backgroundImage: "url('/images/flags/#{@props.country.code}.png')"
