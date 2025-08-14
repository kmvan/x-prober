import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { ServerBenchmarkConstants } from './constants.ts';
import { ServerBenchmark as content } from './index.tsx';
import { ServerBenchmarkNav as nav } from './nav.tsx';
export const ServerBenchmarkLoader: ModuleProps = {
  id: ServerBenchmarkConstants.id,
  content,
  nav,
};
