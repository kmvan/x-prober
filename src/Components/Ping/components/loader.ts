import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { PingConstants } from './constants.ts';
import { Ping as component } from './index.tsx';
import { PingNav as nav } from './nav.tsx';

export const PingLoader = (): CardProps => ({
  id: PingConstants.id,
  title: gettext('Network Ping'),
  priority: 250,
  component,
  nav,
});
