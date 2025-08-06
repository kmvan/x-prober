import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { CardItem } from '@/Components/Card/components/item.tsx';
import { gettext } from '@/Components/Language/index.ts';
import { Meter } from '@/Components/Meter/components/index.tsx';
import { DiskUsageConstants } from './constants.ts';
import styles from './index.module.scss';
import { DiskUsageStore } from './store.ts';
export const DiskUsage: FC = observer(() => {
  const { pollData } = DiskUsageStore;
  const items = pollData?.items ?? [];
  if (!items.length) {
    return null;
  }
  return (
    <CardItem id={DiskUsageConstants.id} title={gettext('Disk Usage')}>
      <div className={styles.main}>
        {items.map(({ id, free, total }) => (
          <Meter
            isCapacity
            key={id}
            max={total}
            name={id}
            value={total - free}
          />
        ))}
      </div>
    </CardItem>
  );
});
