import { FC, memo } from 'react'
import { formatBytes } from '../../Utils/components/format-bytes'
import { gradientColors } from '../../Utils/components/gradient'
import styles from './styles.module.scss'
export interface ProgressBarProps {
  title?: string
  value: number
  max: number
  isCapacity: boolean
  percentTag?: string
  left?: string
}
const MemoProgressBar: FC<ProgressBarProps> = ({
  title = '',
  value,
  max,
  isCapacity,
  percentTag = '%',
  left = '',
}) => {
  const percent = max === 0 || value === 0 ? 0 : (value / max) * 100
  const overview = isCapacity
    ? `${formatBytes(value)} / ${formatBytes(max)}`
    : `${value.toFixed(1)}${percentTag} / ${max}${percentTag}`
  const overviewPercent = left || `${percent.toFixed(1)}${percentTag}`
  return (
    <div className={styles.main} title={title}>
      <div className={[styles.precent, styles.overview].join(' ')}>
        {overviewPercent}
      </div>
      <div className={styles.overview}>{overview}</div>
      <div className={styles.shell}>
        <div
          className={styles.value}
          style={{
            background:
              '#' +
              gradientColors('#00cc00', '#ef2d2d')[Math.round(percent) - 1],
            width: `${percent}%`,
          }}
        />
      </div>
    </div>
  )
}
export const ProgressBar = memo(MemoProgressBar)
