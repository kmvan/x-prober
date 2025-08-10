import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { NavItem } from '@/Components/Nav/components/item.tsx';
import { TemperatureSensorConstants } from './constants.ts';
import { TemperatureSensorStore } from './store.ts';

export const TemperatureSensorNav: FC = observer(() => {
  const { pollData } = TemperatureSensorStore;
  if (!pollData?.items?.length) {
    return null;
  }
  const { items } = pollData;
  if (!items.length) {
    return null;
  }
  return (
    <NavItem
      id={TemperatureSensorConstants.id}
      title={gettext('Temperature')}
    />
  );
});
