import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { Meter } from '@/Components/Meter/components/index.tsx';
import { template } from '@/Components/Utils/components/template';
import { ServerStatusStore } from './store.ts';
export const CpuUsage: FC = observer(() => {
  const { cpuUsage } = ServerStatusStore;
  const { idle } = cpuUsage;
  return (
    <Meter
      isCapacity={false}
      max={100}
      name={gettext('CPU usage')}
      title={template(
        gettext(
          'idle: {{idle}} \nnice: {{nice}} \nsys: {{sys}} \nuser: {{user}}'
        ),
        {
          idle,
          sys: cpuUsage.sys,
          user: cpuUsage.user,
        }
      )}
      value={100 - idle}
    />
  );
});
