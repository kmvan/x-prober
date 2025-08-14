import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { MeterCore } from '@/Components/Meter/components/index.tsx';
import { template } from '@/Components/Utils/components/template';
import { ServerStatusStore } from './store.ts';
import styles from './system-load.module.scss';
export const SysLoadItem: FC<{ load: number; title?: string }> = ({
  load,
  title,
}) => {
  return (
    <div className={styles.groupItem} title={title}>
      {load.toFixed(2)}
    </div>
  );
};
export const SysLoadGroup: FC<{
  sysLoad: number[];
}> = ({ sysLoad }) => {
  const minutes = [1, 5, 15];
  const loadHuman = sysLoad.map((load, i) => ({
    id: `${minutes[i]}minAvg`,
    load,
    text: template(gettext('{{minute}} minute average'), {
      minute: minutes[i],
    }),
  }));
  return (
    <div className={styles.group}>
      {loadHuman.map(({ id, load, text }) => (
        <div className={styles.groupItem} key={id} title={text}>
          {load.toFixed(2)}
        </div>
      ))}
    </div>
  );
};
export const SystemLoad: FC = observer(() => {
  const { sysLoad, cpuUsage } = ServerStatusStore;
  const cpuTotal = cpuUsage.user + cpuUsage.idle + cpuUsage.sys;
  const cpuTitle = `
user: ${((cpuUsage.user / cpuTotal) * 100).toFixed(2)}%
idle: ${((cpuUsage.idle / cpuTotal) * 100).toFixed(2)}%
sys: ${((cpuUsage.sys / cpuTotal) * 100).toFixed(2)}%
`;
  return (
    <div className={styles.main}>
      <div className={styles.label}>{gettext('System load')}</div>
      <SysLoadGroup sysLoad={sysLoad} />
      <div className={styles.usage} title={cpuTitle}>
        {template(gettext('{{usage}}% CPU usage'), { usage: cpuUsage.usage })}
      </div>
      <div className={styles.meter}>
        <MeterCore value={cpuUsage.usage > 100 ? 100 : cpuUsage.usage} />
      </div>
    </div>
  );
});
