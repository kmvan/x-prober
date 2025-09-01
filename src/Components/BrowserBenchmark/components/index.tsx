import { type FC, memo } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { ModuleItem } from '@/Components/Module/components/item.tsx';
import { UiDescription } from '@/Components/ui/description/index.tsx';
import { BrowserBenchmarkConstants } from './constants.ts';
export const BrowserBenchmark: FC = memo(() => {
  return (
    <ModuleItem
      id={BrowserBenchmarkConstants.id}
      title={gettext('Browser Benchmark')}
    >
      {/* <UiDescription
        items={[
          {
            id: 'browserBenchmarkTos',
            text: gettext(
              'Different versions cannot be compared, and different time clients have different loads, just for reference.'
            ),
          },
        ]}
      /> */}
      <UiDescription
        items={[
          {
            id: 'dev',
            text: '(Under development)',
          },
        ]}
      />
    </ModuleItem>
  );
});
