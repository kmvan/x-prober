import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { ServerInfoConstants } from './constants.ts';
import { ServerInfo as component } from './index.tsx';
import { ServerInfoNav as nav } from './nav.tsx';

export const ServerInfoLoader = (): CardProps => ({
  id: ServerInfoConstants.id,
  title: gettext('Server Information'),
  priority: 300,
  component,
  nav,
});
