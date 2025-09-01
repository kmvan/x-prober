import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { ServerBenchmarkConstants } from './constants.ts';
export const ServerBenchmarkNav: FC = () => {
  return (
    <NavItem id={ServerBenchmarkConstants.id} title={gettext('Server bench')} />
  );
};
