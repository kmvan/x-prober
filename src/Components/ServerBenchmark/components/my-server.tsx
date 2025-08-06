import { observer } from 'mobx-react-lite';
import { type MouseEvent, useCallback, useState } from 'react';
import { Button } from '@/Components/Button/components/index.tsx';
import { ButtonStatus } from '@/Components/Button/components/typings.ts';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { gettext } from '@/Components/Language/index.ts';
import { OK, TOO_MANY_REQUESTS } from '@/Components/Rest/http-status.ts';
import { ToastStore } from '@/Components/Toast/components/store.ts';
import { template } from '@/Components/Utils/components/template.ts';
import { ServerBenchmarkItem } from './server-item.tsx';
import { ServerBenchmarkStore } from './store.ts';
import type { ServerBenchmarkMarksProps } from './typings.ts';
export const ServerBenchmarkMyServer = observer(() => {
  const [benchmarking, setBenchmarking] = useState(false);
  const { setMaxMarks, maxMarks } = ServerBenchmarkStore;
  const [marks, setMarks] = useState<ServerBenchmarkMarksProps>({
    cpu: 0,
    read: 0,
    write: 0,
  });
  const handleBenchmarking = useCallback(
    async (e: MouseEvent<HTMLButtonElement>): Promise<void> => {
      e.preventDefault();
      e.stopPropagation();
      if (benchmarking) {
        return;
      }
      // setLinkText(gettext('Testing, please wait...'))
      setBenchmarking(true);
      const { data, status } = await serverFetch<{
        marks: ServerBenchmarkMarksProps;
        seconds: number;
      }>('benchmarkPerformance');
      setBenchmarking(false);
      // const { marks, seconds = 0 } = data || {}
      if (status === OK) {
        if (data?.marks) {
          setMarks(data.marks);
          const total = Object.values(data.marks).reduce((a, b) => a + b, 0);
          if (total > maxMarks) {
            setMaxMarks(total);
          }
          return;
        }
        ToastStore.open(gettext('Network error, please try again later.'));
        return;
      }
      if (data?.seconds && status === TOO_MANY_REQUESTS) {
        ToastStore.open(
          template(gettext('Please wait {{seconds}}s'), {
            seconds: data.seconds,
          })
        );
        return;
      }
      ToastStore.open(gettext('Network error, please try again later.'));
    },
    [benchmarking, maxMarks, setMaxMarks]
  );
  const date = new Date();
  const header = (
    <Button
      onClick={handleBenchmarking}
      status={benchmarking ? ButtonStatus.Loading : ButtonStatus.Pointer}
    >
      {gettext('Benchmark my server')}
    </Button>
  );
  return (
    <ServerBenchmarkItem
      date={`${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`}
      header={header}
      marks={marks}
      maxMarks={maxMarks}
    />
  );
});
