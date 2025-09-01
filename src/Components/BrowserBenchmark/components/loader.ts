import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { BrowserBenchmarkConstants } from './constants.ts';
import { BrowserBenchmark as content } from './index.tsx';
import { BrowserBenchmarkNav as nav } from './nav.tsx';
export const BrowserBenchmarkLoader: ModuleProps = {
  id: BrowserBenchmarkConstants.id,
  content,
  nav,
};
