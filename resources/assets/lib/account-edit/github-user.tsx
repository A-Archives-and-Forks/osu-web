// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import BigButton from 'components/big-button';
import GithubUserJson from 'interfaces/github-user-json';
import { route } from 'laroute';
import { action, makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import * as React from 'react';
import { onErrorWithCallback } from 'utils/ajax';
import { trans } from 'utils/lang';

interface Props {
  user: GithubUserJson | null | undefined;
}

@observer
export default class GithubUsers extends React.Component<Props> {
  @observable private deleting = false;
  @observable private user: GithubUserJson | null | undefined;
  private xhr?: JQuery.jqXHR;

  constructor(props: Props) {
    super(props);

    this.user = props.user;

    makeObservable(this);
  }

  componentWillUnmount() {
    this.xhr?.abort();
  }

  render() {
    return (
      <div className='github-user'>
        {this.user != null ? (
          <>
            <a
              className='github-user__name'
              href={this.user.github_url}
            >
              {this.user.github_username}
            </a>
            <BigButton
              icon='fas fa-trash'
              isBusy={this.deleting}
              modifiers={['account-edit', 'danger', 'settings-github']}
              props={{ onClick: this.onDeleteButtonClick }}
              text={trans('common.buttons.delete')}
            />
          </>
        ) : (
          <BigButton
            href={route('account.github-users.create')}
            icon='fas fa-link'
            text={trans('accounts.github_user.link')}
          />
        )}
      </div>
    );
  }

  @action
  private onDeleteButtonClick = () => {
    this.xhr?.abort();
    this.deleting = true;

    this.xhr = $.ajax(
      route('account.github-users.destroy', { github_user: this.user?.id }),
      { method: 'DELETE' },
    )
      .done(() => this.user = null)
      .fail(onErrorWithCallback(this.onDeleteButtonClick))
      .always(action(() => this.deleting = false));
  };
}
