import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { DatabaseConstants } from './constants.ts';export const DatabaseNav: FC = () => {
  return <NavItem id={DatabaseConstants.id} title={gettext('DB')} />;
};
