import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { PhpExtensionsConstants } from './constants.ts';
import { PhpExtensionsStore } from './store.ts';export const PhpExtensionsNav: FC = observer(() => {
  const { pollData } = PhpExtensionsStore;
  if (!pollData) {
    return null;
  }
  return <NavItem id={PhpExtensionsConstants.id} title={gettext('Ext')} />;
});
