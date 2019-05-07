/**
 *    Copyright (c) ppy Pty Ltd <contact@ppy.sh>.
 *
 *    This file is part of osu!web. osu!web is distributed with the hope of
 *    attracting more community contributions to the core ecosystem of osu!.
 *
 *    osu!web is free software: you can redistribute it and/or modify
 *    it under the terms of the Affero GNU General Public License version 3
 *    as published by the Free Software Foundation.
 *
 *    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
 *    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *    See the GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
 */

import HeaderV3 from 'header-v3';
import * as React from 'react';
import { Main as UserList } from 'user-list/main';

interface Group {
  group_name: string;
  group_desc?: string;
}

interface Props {
  group: Group;
  users: User[];
}

export class Main extends React.PureComponent<Props> {
  render() {
    return (
      <div className='osu-layout osu-layout--full'>
        <HeaderV3
          backgroundImage='/images/headers/generic.jpg'
          theme='users'
          title={this.props.group.group_name}
        />

        <div className='osu-page osu-page--users'>
          <UserList users={this.props.users} />
        </div>
      </div>
    );
  }
}
