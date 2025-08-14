import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { Meter } from '@/Components/Meter/components/index.tsx';
import { ServerStatusStore } from './store.ts';
export const MemRealUsage: FC = observer(() => {
  const { max, value } = ServerStatusStore.memRealUsage;
  return (
    <Meter
      isCapacity
      max={max}
      name={gettext('Memory real usage')}
      title={gettext(
        'Linux comes with many commands to check memory usage. The "free" command usually displays the total amount of free and used physical and swap memory in the system, as well as the buffers used by the kernel. The "top" command provides a dynamic real-time view of a running system.'
      )}
      value={value}
    />
  );
});
