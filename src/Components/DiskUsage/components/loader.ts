import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { DiskUsage as component } from '.';
import { DiskUsageConstants } from './constants';
import { DiskUsageNav as nav } from './nav';
export const DiskUsageLoader = (): CardProps => ({
  id: DiskUsageConstants.id,
  title: gettext('Disk usage'),
  priority: 250,
  component,
  nav,
});
