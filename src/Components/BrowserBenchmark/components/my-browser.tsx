import { observer } from 'mobx-react-lite';
import { type MouseEvent, useCallback, useState } from 'react';
import { Button } from '@/Components/Button/components/index.tsx';
import { ButtonStatus } from '@/Components/Button/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { BrowserBenchmarkItem } from './browsers-item.tsx';
import { BrowserBenchmarkStore } from './store.ts';
import { BrowserBenchmarkTests } from './tests.ts';
import type { BrowserBenchmarkMarksProps } from './typings.ts';
export const BrowserBenchmarkMyBrowser = observer(() => {
  const [benchmarking, setBenchmarking] = useState(false);
  const { setMaxMarks, maxMarks } = BrowserBenchmarkStore;
  const [marks, setMarks] = useState<BrowserBenchmarkMarksProps>({
    js: 0,
    dom: 0,
    canvas: 0,
  });
  const handleBenchmarking = useCallback(
    (e: MouseEvent<HTMLButtonElement>) => {
      e.preventDefault();
      e.stopPropagation();
      if (benchmarking) {
        return;
      }
      if (
        !window.confirm(
          gettext(
            'Running the benchmark may freeze the browser interface for a few seconds. Do you want to continue?'
          )
        )
      ) {
        return;
      }
      setBenchmarking(true);
      const results = {
        js: BrowserBenchmarkTests.runJs(),
        dom: BrowserBenchmarkTests.runDom(),
        canvas: BrowserBenchmarkTests.runCanvas(),
      };
      setBenchmarking(false);
      setMarks(results);
      const total = Object.values(results).reduce((a, b) => a + b, 0);
      if (total > maxMarks) {
        setMaxMarks(total);
      }
    },
    [benchmarking, maxMarks, setMaxMarks]
  );
  const date = new Date();
  const header = (
    <Button
      disabled={benchmarking}
      onClick={handleBenchmarking}
      status={benchmarking ? ButtonStatus.Loading : ButtonStatus.Pointer}
    >
      {gettext('Benchmark my browser')}
    </Button>
  );
  return (
    <BrowserBenchmarkItem
      date={`${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`}
      header={header}
      marks={marks}
      maxMarks={maxMarks}
    />
  );
});
