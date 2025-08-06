import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { DatabaseConstants } from './constants.ts';
import { Database as component } from './index.tsx';
import { DatabaseNav as nav } from './nav.tsx';
export const DatabaseLoader = (): CardProps => ({
  id: DatabaseConstants.id,
  title: gettext('Database'),
  priority: 600,
  component,
  nav,
});
