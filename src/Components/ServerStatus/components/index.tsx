import { FC } from 'react'
import { GridContainer } from '../../Grid/components/container'
import { CpuUsage } from './cpu-usage'
import { MemBuffers } from './mem-buffers'
import { MemCached } from './mem-cached'
import { MemRealUsage } from './mem-real-usage'
import { SwapCached } from './swap-cached'
import { SwapUsage } from './swap-usage'
import { SystemLoad } from './system-load'
export const ServerStatus: FC = () => (
  <GridContainer>
    <SystemLoad />
    <CpuUsage />
    <MemRealUsage />
    <MemCached />
    <MemBuffers />
    <SwapUsage />
    <SwapCached />
  </GridContainer>
)
