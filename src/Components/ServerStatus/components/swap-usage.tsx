import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { Meter } from '@/Components/Meter/components/index.tsx';
import { ServerStatusStore } from './store.ts';
export const SwapUsage: FC = observer(() => {
  const { max, value } = ServerStatusStore.swapUsage;
  if (!max) {
    return null;
  }
  return (
    <Meter isCapacity max={max} name={gettext('Swap usage')} value={value} />
  );
});
