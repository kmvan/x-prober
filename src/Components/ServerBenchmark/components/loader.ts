import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { ServerBenchmarkConstants } from './constants.ts';
import { ServerBenchmark as component } from './index.tsx';
import { ServerBenchmarkNav as nav } from './nav.tsx';
export const ServerBenchmarkLoader = (): CardProps => ({
  id: ServerBenchmarkConstants.id,
  title: gettext('Server Benchmark'),
  priority: 800,
  component,
  nav,
});
