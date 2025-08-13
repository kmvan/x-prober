import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { NetworkStatsConstants } from './constants.ts';
import { NetworkStats as content } from './index.tsx';
import { NetworkStatsNav as nav } from './nav.tsx';export const NetworkStatsLoader: ModuleProps = {
  id: NetworkStatsConstants.id,
  content,
  nav,
};
