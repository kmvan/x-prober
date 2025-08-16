import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { Meter } from '@/Components/Meter/components/index.tsx';
import { ServerStatusStore } from './store.ts';
export const MemCached: FC = observer(() => {
  const { max, value } = ServerStatusStore.memCached;
  return (
    <Meter
      isCapacity
      max={max}
      name={gettext('Memory cached')}
      title={gettext(
        'Cached memory is memory that Linux uses for disk caching. However, this does not count as "used" memory, since it will be freed when applications require it. Hence you do not have to worry if a large amount is being used.'
      )}
      value={value}
    />
  );
});
