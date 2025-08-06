import type { FC, HTMLProps, ReactNode } from 'react';
import { MeterCore } from '@/Components/Meter/components/index.tsx';
import styles from './usage.module.scss';
export const NodesUsage: FC<{ children: ReactNode; percent: number }> = ({
  children,
  percent,
}) => {
  return (
    <div className={styles.main}>
      {children}
      <div className={styles.percent}>{percent}%</div>
      <div className={styles.meter}>
        <MeterCore max={100} value={percent} />
      </div>
    </div>
  );
};
export const NodesUsageLabel: FC<{ children: ReactNode }> = (props) => {
  return <div className={styles.label} {...props} />;
};
export const NodesUsageChart: FC<{ children: ReactNode }> = (props) => {
  return <div className={styles.chart} {...props} />;
};
export const NodesUsageOverview: FC<HTMLProps<HTMLDivElement>> = (props) => {
  return <div className={styles.overview} {...props} />;
};
// export const NodesUsagePercent: FC<{ percent: number }> = ({ percent }) => {
//   return <div className={styles.percent}>{percent}%</div>
// }
