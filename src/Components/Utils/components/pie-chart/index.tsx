import type { FC } from 'react';
import styles from './index.module.scss';
import type { PieChartStatus } from './typings.ts';
export const PieChart: FC<{
  percent: number;
  status: PieChartStatus;
  size?: number;
  stockWidth?: number;
  fontSize?: number;
}> = ({ percent, status, size = 25, stockWidth = 2, fontSize = 10 }) => {
  const circumference = 2 * Math.PI * size;
  const strokeDashoffset = circumference - (percent / 100) * circumference;
  return (
    <svg
      height={size * 2}
      viewBox={`0 0 ${size * 2} ${size * 2}`}
      width={size * 2}
    >
      <title>Pie chart</title>
      <circle
        className={styles.pieBg}
        cx={size}
        cy={size}
        fill="none"
        r={size - stockWidth / 2}
        strokeWidth={stockWidth}
      />
      <circle
        className={styles.pieFg}
        cx={size}
        cy={size}
        data-status={status}
        fill="none"
        r={size - stockWidth / 2}
        strokeDasharray={circumference}
        strokeDashoffset={strokeDashoffset}
        strokeWidth={stockWidth}
        transform={`rotate(-90 ${size} ${size})`}
      />
      <text
        className={styles.pipText}
        dominantBaseline="middle"
        fontSize={fontSize}
        textAnchor="middle"
        x={size}
        y={size}
      >
        {percent}%
      </text>
    </svg>
  );
};
