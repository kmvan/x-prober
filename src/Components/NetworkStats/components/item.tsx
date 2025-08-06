import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { formatBytes } from '@/Components/Utils/components/format-bytes';
import { template } from '@/Components/Utils/components/template.ts';
import styles from './item.module.scss';

interface NetworksStatsItemProps {
  id: string;
  totalRx: number;
  rateRx: number;
  totalTx: number;
  rateTx: number;
}
export const NetworksStatsItem: FC<NetworksStatsItemProps> = ({
  id,
  totalRx = 0,
  rateRx = 0,
  totalTx = 0,
  rateTx = 0,
}) => {
  if (!id) {
    return null;
  }
  return (
    <div className={styles.main}>
      <div className={styles.id}>{id}</div>
      <div className={styles.rx}>
        <div className={styles.type}>
          {template(gettext('Recived: {{total}}'), {
            total: formatBytes(totalRx),
          })}
        </div>
        <div className={styles.rateRx}>{formatBytes(rateRx)}/s</div>
      </div>
      <div className={styles.tx}>
        <div className={styles.type}>
          {template(gettext('Sent: {{total}}'), {
            total: formatBytes(totalTx),
          })}
        </div>
        <div className={styles.rateTx}>{formatBytes(rateTx)}/s</div>
      </div>
    </div>
  );
};
