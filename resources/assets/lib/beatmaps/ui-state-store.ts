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

import { BeatmapSearchFilters, BeatmapSearchParams } from 'beatmap-search-filters';
import { intersection, map } from 'lodash';
import { action, computed, observable } from 'mobx';
import core from 'osu-core-singleton';

const store = core.dataStore.beatmapSearchStore;

export interface SearchStatus {
  error?: any;
  from: number;
  state: 'completed' // search not doing anything
    | 'input'        // receiving input but not searching
    | 'paging'       // getting more pages
    | 'searching'    // actually doing a search
    ;
}

class UIStateStore {
  // the list that gets displaying while new searches are loading.
  @observable currentBeatmapsets!: any[];
  @observable hasMore = false; // TODO: figure out how to make this computed
  @observable numberOfColumns = osu.isDesktop() ? 2 : 1;
  @observable recommendedDifficulty = store.recommendedDifficulty;
  @observable filters!: BeatmapSearchFilters;
  @observable isExpanded!: boolean;
  @observable searchStatus: SearchStatus = {
    error: null,
    from: 0,
    state: 'completed',
  };

  constructor() {
    this.restoreStateFromUrl();
    this.currentBeatmapsets = store.getBeatmapsets(this.filters);
  }

  @computed
  get error() {
    return this.searchStatus.error;
  }

  @computed
  get isBusy() {
    return this.searchStatus.state === 'searching' || this.searchStatus.state === 'input';
  }

  @computed
  get isPaging() {
    return this.searchStatus.state === 'paging';
  }

  @computed
  get isSupporterMissing() {
    return !currentUser.is_supporter && BeatmapsetFilter.supporterRequired(this.filters).length > 0;
  }

  @computed
  get supporterRequiredFilterText() {
    const filters = BeatmapsetFilter.supporterRequired(this.filters);
    const trans = map(filters, (name) => osu.trans(`beatmaps.listing.search.filters.${name}`));

    return osu.transArray(trans);
  }

  cancel() {
    store.cancel();
  }

  @action
  async loadMore() {
    if (this.isBusy || !this.hasMore) {
      return;
    }

    this.search(this.currentBeatmapsets.length);
  }

  @action
  prepareToSearch() {
    this.searchStatus.state = 'input';
  }

  @action
  restoreTurbolinks() {
    this.restoreStateFromUrl();
    this.search();
  }

  @action
  async search(from = 0) {
    if (this.isSupporterMissing || from < 0) {
      return;
    }

    this.searchStatus = {
      from,
      state: from === 0 ? 'searching' : 'paging',
    };

    try {
      const data = await store.get(this.filters, from);

      this.searchStatus = { state: 'completed', error: null, from };
      this.hasMore = data.hasMore && data.beatmapsets.length < data.total;
      this.recommendedDifficulty = data.recommendedDifficulty;

      this.currentBeatmapsets = store.getBeatmapsets(this.filters);
    } catch (error) {
      if (error.readyState !== 0) {
        this.searchStatus = { state: 'completed', error, from };
      } else {
        this.searchStatus = { state: 'completed', error: null, from };
      }
    }
  }

  @action
  updateFilters(newFilters: Partial<BeatmapSearchParams>) {
    this.filters.update(newFilters);
  }

  startListeningOnWindow() {
    $(window).on('resize.beatmaps-ui-state-store', () => {
      const count = osu.isDesktop() ? 2 : 1;
      if (this.numberOfColumns !== count) {
        this.numberOfColumns = count;
      }
    });
  }

  stopListeningOnWindow() {
    $(window).off('.beatmaps-ui-state-store');
  }

  private restoreStateFromUrl() {
    const filtersFromUrl = BeatmapsetFilter.filtersFromUrl(location.href);
    this.filters = new BeatmapSearchFilters(location.href),
    this.isExpanded = intersection(Object.keys(filtersFromUrl), BeatmapsetFilter.expand).length > 0;
  }
}

export const uiState = new UIStateStore();
