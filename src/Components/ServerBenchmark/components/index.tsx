import { type FC, memo } from 'react';
import { CardDescription } from '@/Components/Card/components/description.tsx';
import { CardItem } from '@/Components/Card/components/item.tsx';
import { gettext } from '@/Components/Language/index.ts';
import { ServerBenchmarkConstants } from './constants.ts';
import { ServerBenchmarkServers } from './servers.tsx';
export const ServerBenchmark: FC = memo(() => {
  return (
    <CardItem
      id={ServerBenchmarkConstants.id}
      title={gettext('Server Benchmark')}
    >
      <CardDescription
        items={[
          {
            id: 'serverBenchmarkTos',
            text: gettext(
              'Different versions cannot be compared, and different time servers have different loads, just for reference.'
            ),
          },
        ]}
      />
      <ServerBenchmarkServers />
    </CardItem>
  );
});
