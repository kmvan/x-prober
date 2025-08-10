import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { ModuleItem } from '@/Components/Module/components/item.tsx';
import { ServerStatusConstants } from './constants.ts';
import styles from './index.module.scss';
import { MemBuffers } from './mem-buffers';
import { MemCached } from './mem-cached';
import { MemRealUsage } from './mem-real-usage';
import { SwapCached } from './swap-cached';
import { SwapUsage } from './swap-usage';
import { SystemLoad } from './system-load';
export const ServerStatus: FC = () => (
  <ModuleItem id={ServerStatusConstants.id} title={gettext('Server Status')}>
    <div className={styles.main}>
      <div className={styles.modules}>
        <SystemLoad />
        <MemRealUsage />
        <MemCached />
        <MemBuffers />
        <SwapUsage />
        <SwapCached />
      </div>
    </div>
  </ModuleItem>
);
