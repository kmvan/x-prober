import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { NodesConstants } from './constants.ts';
import { Nodes as component } from './index.tsx';
import { NodesNav as nav } from './nav.tsx';
export const NodesLoader = (): CardProps => ({
  id: NodesConstants.id,
  title: gettext('Nodes'),
  priority: 90,
  component,
  nav,
});
