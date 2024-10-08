// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import Ruleset from 'interfaces/ruleset';
import SoloScoreJson, { SoloScoreStatisticsAttribute } from 'interfaces/solo-score-json';
import { route } from 'laroute';
import core from 'osu-core-singleton';
import { rulesetName } from './beatmap-helper';
import { trans } from './lang';
import { legacyAccuracyAndRank } from './legacy-score-helper';

export function accuracy(score: SoloScoreJson) {
  return shouldReturnLegacyValue(score)
    ? legacyAccuracyAndRank(score).accuracy
    : score.accuracy;
}

export function canBeReported(score: SoloScoreJson) {
  return (score.best_id != null || score.type === 'solo_score')
    && core.currentUser != null
    && score.user_id !== core.currentUser.id;
}

// Removes CL mod on legacy score if user has lazer mode disabled
export function filterMods(score: SoloScoreJson) {
  return shouldReturnLegacyValue(score)
    ? score.mods.filter((mod) => mod.acronym !== 'CL')
    : score.mods;

}

// TODO: move to application state repository thingy later
export function hasMenu(score: SoloScoreJson) {
  return canBeReported(score) || hasReplay(score) || hasShow(score) || core.scorePins.canBePinned(score);
}

export function hasReplay(score: SoloScoreJson) {
  return score.has_replay;
}

export function hasShow(score: SoloScoreJson) {
  return score.best_id != null || score.type === 'solo_score';
}

export function isPerfectCombo(score: SoloScoreJson) {
  return shouldReturnLegacyValue(score)
    ? score.legacy_perfect
    : score.is_perfect_combo;
}

interface AttributeDisplayMapping {
  attributes: SoloScoreStatisticsAttribute[];
  key: string;
  label: string;
}

interface AttributeDisplayTotal {
  key: string;
  label: string;
  total: number;
}

const labelMiss = trans('beatmapsets.show.scoreboard.headers.miss');

export const modeAttributesMap: Record<Ruleset, AttributeDisplayMapping[]> = {
  fruits: [
    { attributes: ['great'], key: 'great', label: 'fruits' },
    { attributes: ['large_tick_hit'], key: 'ticks', label: 'ticks' },
    { attributes: ['small_tick_miss'], key: 'drp_miss', label: 'drp miss' },
    // legacy/stable scores merge miss and large_tick_miss into one number
    { attributes: ['miss', 'large_tick_miss'], key: 'miss', label: labelMiss },
  ],
  mania: [
    { attributes: ['perfect'], key: 'perfect', label: 'max' },
    { attributes: ['great'], key: 'great', label: '300' },
    { attributes: ['good'], key: 'good', label: '200' },
    { attributes: ['ok'], key: 'ok', label: '100' },
    { attributes: ['meh'], key: 'meh', label: '50' },
    { attributes: ['miss'], key: 'miss', label: labelMiss },
  ],
  osu: [
    { attributes: ['great'], key: 'great', label: '300' },
    { attributes: ['ok'], key: 'ok', label: '100' },
    { attributes: ['meh'], key: 'meh', label: '50' },
    { attributes: ['miss'], key: 'miss', label: labelMiss },
  ],
  taiko: [
    { attributes: ['great'], key: 'great', label: 'great' },
    { attributes: ['ok'], key: 'ok', label: 'good' },
    { attributes: ['miss'], key: 'miss', label: labelMiss },
  ],
};

export function attributeDisplayTotals(ruleset: Ruleset, score: SoloScoreJson): AttributeDisplayTotal[] {
  return modeAttributesMap[ruleset].map((mapping) => ({
    key: mapping.key,
    label: mapping.label,
    total: mapping.attributes.reduce((sum, attribute) => sum + (score.statistics[attribute] ?? 0), 0),
  }));
}

export function rank(score: SoloScoreJson) {
  return shouldReturnLegacyValue(score)
    ? legacyAccuracyAndRank(score).rank
    : score.rank;
}

export function rankCutoffs(score: SoloScoreJson): number[] {
  // <rank>: minimum acc => (higher rank acc - current acc)
  // for SS, use minimum accuracy of 0.99 (any less and it's too small)
  // actual array is reversed as it's rendered from D to SS clockwise

  if (shouldReturnLegacyValue(score)) {
    return {
      // SS: 0.99 => 0.01
      // S: 0.9801 => 0.0099
      // A: 0.9401 => 0.04
      // B: 0.9001 => 0.04
      // C: 0.8501 => 0.05
      // D: 0 => 0.8501
      fruits: [0.8501, 0.05, 0.04, 0.04, 0.0099, 0.01],
      // SS: 0.99 => 0.01
      // S: 0.95 => 0.04
      // A: 0.9 => 0.05
      // B: 0.8 => 0.1
      // C: 0.7 => 0.1
      // D: 0 => 0.7
      mania: [0.7, 0.1, 0.1, 0.05, 0.04, 0.01],
      // SS: 0.99 => 0.01
      // S: (0.9 * 300 + 0.1 * 100) / 300 = 0.933 => 0.057
      // A: (0.8 * 300 + 0.2 * 100) / 300 = 0.867 => 0.066
      // B: (0.7 * 300 + 0.3 * 100) / 300 = 0.8 => 0.067
      // C: 0.6 => 0.2
      // D: 0 => 0.6
      osu: [0.6, 0.2, 0.067, 0.066, 0.057, 0.01],
      // SS: 0.99 => 0.01
      // S: (0.9 * 300 + 0.1 * 50) / 300 = 0.917 => 0.073
      // A: (0.8 * 300 + 0.2 * 50) / 300 = 0.833 => 0.084
      // B: (0.7 * 300 + 0.3 * 50) / 300 = 0.75 => 0.083
      // C: 0.6 => 0.15
      // D: 0 => 0.6
      taiko: [0.6, 0.15, 0.083, 0.084, 0.073, 0.01],
    }[rulesetName(score.ruleset_id)];
  }

  return {
    // SS: 0.99 => 0.01
    // S: 0.98 => 0.01
    // A: 0.94 => 0.04
    // B: 0.9 => 0.04
    // C: 0.85 => 0.05
    // D: 0 => 0.85
    // cross-reference: https://github.com/ppy/osu/blob/b658d9a681a04101900d5ce6c5b84d56320e08e7/osu.Game.Rulesets.Catch/Scoring/CatchScoreProcessor.cs#L108-L135
    fruits: [0.85, 0.05, 0.04, 0.04, 0.01, 0.01],
    // remaining rulesets use the same cutoffs
    // SS: 0.99 => 0.01
    // S: 0.95 => 0.04
    // A: 0.9 => 0.05
    // B: 0.8 => 0.1
    // C: 0.7 => 0.1
    // D: 0 => 0.7
    // cross-reference: https://github.com/ppy/osu/blob/b658d9a681a04101900d5ce6c5b84d56320e08e7/osu.Game/Rulesets/Scoring/ScoreProcessor.cs#L541-L572
    mania: [0.7, 0.1, 0.1, 0.05, 0.04, 0.01],
    osu: [0.7, 0.1, 0.1, 0.05, 0.04, 0.01],
    taiko: [0.7, 0.1, 0.1, 0.05, 0.04, 0.01],
  }[rulesetName(score.ruleset_id)];
}

export function scoreDownloadUrl(score: SoloScoreJson) {
  if (score.type === 'solo_score') {
    return route('scores.download', { score: score.id });
  }

  if (score.best_id != null) {
    return route('scores.download-legacy', {
      rulesetOrScore: rulesetName(score.ruleset_id),
      score: score.best_id,
    });
  }

  throw new Error('score json doesn\'t have download url');
}

export function scoreUrl(score: SoloScoreJson) {
  if (score.type === 'solo_score') {
    return route('scores.show', { rulesetOrScore: score.id });
  }

  if (score.best_id != null) {
    return route('scores.show', {
      rulesetOrScore: rulesetName(score.ruleset_id),
      score: score.best_id,
    });
  }

  throw new Error('score json doesn\'t have url');
}

function shouldReturnLegacyValue(score: SoloScoreJson) {
  return score.legacy_score_id !== null && core.userPreferences.get('legacy_score_only');
}

export function totalScore(score: SoloScoreJson) {
  if (shouldReturnLegacyValue(score)) {
    return score.legacy_total_score;
  }

  if (score.type === 'solo_score' && core.userPreferences.get('scoring_mode') === 'classic') {
    return score.classic_total_score;
  }

  return score.total_score;
}
