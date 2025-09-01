import { MeterCore } from '@/Components/Meter/components/index.tsx';
import styles from './marks-meter.module.scss';
export const BrowserBenchmarkMarksMeter = ({
  totalMarks,
  total,
}: {
  totalMarks: number;
  total: number;
}) => {
  return (
    <div className={styles.main}>
      <MeterCore
        // className={styles.main}
        high={totalMarks * 0.7}
        low={totalMarks * 0.5}
        max={totalMarks}
        optimum={totalMarks}
        value={total}
      />
    </div>
  );
};
