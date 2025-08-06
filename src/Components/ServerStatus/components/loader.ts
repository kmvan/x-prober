import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { ServerStatusConstants } from './constants.ts';
import { ServerStatus as component } from './index.tsx';
import { ServerStatusNav as nav } from './nav.tsx';
export const ServerStatusLoader = (): CardProps => ({
  id: ServerStatusConstants.id,
  title: gettext('Server Status'),
  priority: 100,
  component,
  nav,
});
