import { observer } from 'mobx-react-lite';
import { type FC, useEffect, useState } from 'react';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { gettext } from '@/Components/Language/index.ts';
import { Placeholder } from '@/Components/Placeholder/index.tsx';
import { OK } from '@/Components/Rest/http-status.ts';
import { UiError } from '@/Components/ui/error/index.tsx';
import { BrowserBenchmarkItem } from './browsers-item.tsx';
import styles from './index.module.scss';
import { BrowserBenchmarkMyBrowser } from './my-browser.tsx';
import { BrowserBenchmarkStore } from './store.ts';
import type { BrowserBenchmarkProps } from './typings.ts';
export const BrowserBenchmarkBrowsers: FC = observer(() => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);
  const { browsers, setBrowsers, setMaxMarks, maxMarks } =
    BrowserBenchmarkStore;
  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      const { data, status } =
        await serverFetch<BrowserBenchmarkProps[]>('benchmarkBrowsers');
      setLoading(false);
      if (!data?.length || status !== OK) {
        setError(true);
        return;
      }
      setError(false);
      let marks = 0;
      setBrowsers(
        data
          .map((item) => {
            item.total = item.detail
              ? Object.values(item.detail).reduce((a, b) => a + b, 0)
              : 0;
            if (item.total > marks) {
              marks = item.total;
            }
            return item;
          })
          .toSorted((a, b) => (b?.total ?? 0) - (a?.total ?? 0))
      );
      setMaxMarks(marks);
    };
    fetchData();
  }, [setBrowsers, setMaxMarks]);
  // const maxMarks = browsers.reduce((a, b) => Math.max(a, b?.total ?? 0), 0)
  const results = browsers.map(({ name, version, ua, detail, date }) => {
    if (!detail) {
      return null;
    }
    const {
      sunSpider = 0,
      hash = 0,
      object = 0,
      cssAnimation = 0,
      gc = 0,
      canvas = 0,
      webgl = 0,
    } = detail;
    return (
      <BrowserBenchmarkItem
        date={date}
        header={
          <>
            {name} {version}
          </>
        }
        key={name}
        marks={{ sunSpider, hash, object, cssAnimation, gc, canvas, webgl }}
        maxMarks={maxMarks}
      />
    );
  });
  return (
    <div className={styles.browsers}>
      <BrowserBenchmarkMyBrowser />
      {loading
        ? [...new Array(5)].map(() => <Placeholder key={Math.random()} />)
        : results}
      {error && (
        <UiError>{gettext('Can not fetch marks data from GitHub.')}</UiError>
      )}
    </div>
  );
});
