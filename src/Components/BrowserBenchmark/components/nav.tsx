import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { BrowserBenchmarkConstants } from './constants.ts';
export const BrowserBenchmarkNav: FC = () => {
  return (
    <NavItem
      id={BrowserBenchmarkConstants.id}
      title={gettext('Browser bench')}
    />
  );
};
