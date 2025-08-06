import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { NetworkStatsConstants } from './constants.ts';
import { NetworkStats as component } from './index.tsx';
import { NetworkStatsNav as nav } from './nav.tsx';

export const NetworkStatsLoader = (): CardProps => ({
  id: NetworkStatsConstants.id,
  title: gettext('Network Stats'),
  priority: 200,
  component,
  nav,
});
