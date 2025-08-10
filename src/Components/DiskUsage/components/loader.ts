import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { DiskUsage as content } from '.';
import { DiskUsageConstants } from './constants';
import { DiskUsageNav as nav } from './nav';
export const DiskUsageLoader: ModuleProps = {
  id: DiskUsageConstants.id,
  content,
  nav,
};
