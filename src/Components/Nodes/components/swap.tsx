import { type FC, memo } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import type { ServerStatusUsageProps } from '@/Components/ServerStatus/components/typings.ts';
import { formatBytes } from '@/Components/Utils/components/format-bytes.ts';
import { NodesUsage, NodesUsageLabel, NodesUsageOverview } from './usage.tsx';
export const NodesSwap: FC<{ data: ServerStatusUsageProps }> = memo(
  ({ data }) => {
    const { value, max } = data;
    const percent = max ? Math.round((value / max) * 100) : 0;
    return (
      <NodesUsage percent={percent}>
        <NodesUsageLabel>{`🐏 ${gettext('Swap')}`}</NodesUsageLabel>
        <NodesUsageOverview>{`${formatBytes(value)} / ${formatBytes(max)}`}</NodesUsageOverview>
      </NodesUsage>
    );
  }
);
