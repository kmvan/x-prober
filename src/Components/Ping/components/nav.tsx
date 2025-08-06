import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { PingConstants } from './constants.ts';

export const PingNav: FC = () => {
  return <NavItem id={PingConstants.id} title={gettext('Ping')} />;
};
