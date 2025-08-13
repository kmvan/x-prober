import { type FC, memo } from 'react';
import type { DiskUsageItemProps } from '@/Components/DiskUsage/components/typings.ts';
import type { PollDataProps } from '@/Components/Poll/components/typings.ts';
import { formatBytes } from '@/Components/Utils/components/format-bytes.ts';
import styles from './disk.module.scss';
import { NodesUsage, NodesUsageLabel, NodesUsageOverview } from './usage.tsx';const Disk: FC<DiskUsageItemProps> = memo(({ id, free, total }) => {
  return (
    <div className={styles.item} key={id}>
      <NodesUsage percent={total ? Math.round((free / total) * 100) : 0}>
        <NodesUsageLabel>{`🖴 ${id}`}</NodesUsageLabel>
        <NodesUsageOverview>{`${formatBytes(free)} / ${formatBytes(total)}`}</NodesUsageOverview>
      </NodesUsage>
    </div>
  );
});
export const NodesDisk: FC<{ data: PollDataProps['diskUsage'] }> = ({
  data,
}) => {
  const items = data?.items ?? [];
  return (
    <div className={styles.main}>
      {items.map(({ id, free, total }) => (
        <Disk free={free} id={id} key={id} total={total} />
      ))}
    </div>
  );
};
