import {
  type FC,
  type MouseEvent,
  memo,
  type ReactNode,
  useCallback,
} from 'react';
import { ToastStore } from '@/Components/Toast/components/store';
import { formatBytes } from '@/Components/Utils/components/format-bytes';
import styles from './index.module.scss';
export const MeterCore: FC<{
  value: number;
  max?: number;
  low?: number;
  high?: number;
  optimum?: number;
}> = memo(({ value, max = 100, low = 60, optimum, high = 80 }) => {
  return (
    <meter
      className={styles.core}
      high={high}
      low={low}
      max={max}
      optimum={optimum}
      value={value}
    />
  );
});
const MemoMeter: FC<{
  title?: string;
  name?: string;
  value: number;
  max: number;
  isCapacity: boolean;
  percentTag?: string;
  left?: string;
  percent?: number;
  percentRender?: ReactNode;
  progressPercent?: number;
  children?: ReactNode;
}> = ({
  title,
  name = '',
  value,
  max,
  isCapacity,
  percentTag = '%',
  percent,
  percentRender,
  progressPercent,
}) => {
  const handleNameClick = useCallback(
    (e: MouseEvent<HTMLButtonElement>) => {
      e.preventDefault();
      e.stopPropagation();
      const content = title || name;
      ToastStore.open(content);
      if (title?.length ?? 0 >= 20) {
        return;
      }
      navigator.clipboard.writeText(name);
    },
    [name, title]
  );
  const percentFallback = max === 0 || value === 0 ? 0 : (value / max) * 100;
  const overview = isCapacity
    ? `${formatBytes(value)} / ${formatBytes(max)}`
    : `${value.toFixed(1)}${percentTag} / ${max}${percentTag}`;
  return (
    <div className={styles.main} title={title}>
      <div className={styles.percent}>
        {percentRender ?? `${(percent ?? percentFallback).toFixed(1)}%`}
      </div>
      <button
        className={styles.name}
        onClick={handleNameClick}
        title={name}
        type="button"
      >
        <div className={styles.nameText}>{name}</div>
      </button>
      <div className={styles.overview}>{overview}</div>
      <MeterCore value={progressPercent ?? percentFallback} />
    </div>
  );
};
export const Meter = memo(MemoMeter);
