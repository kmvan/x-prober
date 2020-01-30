import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'
import { observable, configure, action, computed } from 'mobx'
import FetchStore from '~components/Fetch/src/stores'

configure({
  enforceActions: 'observed',
})

export interface ServerStatusUsageProps {
  max: number
  value: number
}

export interface ServerStatusCpuUsageProps {
  idle: number
  nice: number
  sys: number
  user: number
}

class ServerStatus {
  public readonly ID = 'serverStatus'
  public readonly conf = get(conf, this.ID)

  @observable public memRealUsage: ServerStatusUsageProps = this.conf
    .memRealUsage
  @observable public memBuffers: ServerStatusUsageProps = this.conf.memBuffers
  @observable public memCached: ServerStatusUsageProps = this.conf.memCached
  @observable public swapUsage: ServerStatusUsageProps = this.conf.swapUsage
  @observable public swapCached: ServerStatusUsageProps = this.conf.swapCached

  @computed
  public get sysLoad(): number[] {
    return FetchStore.isLoading
      ? get(this.conf, 'sysLoad')
      : get(FetchStore.data, `${this.ID}.sysLoad`) || [0, 0, 0]
  }

  @computed
  public get cpuUsage(): ServerStatusCpuUsageProps {
    return FetchStore.isLoading
      ? {
          idle: 90,
          nice: 0,
          sys: 5,
          user: 5,
        }
      : get(FetchStore.data, `${this.ID}.cpuUsage`)
  }

  @action
  public setMemRealUsage = (memRealUsage: ServerStatusUsageProps) => {
    this.memRealUsage = memRealUsage
  }

  @action
  public setMemBuffers = (memBuffers: ServerStatusUsageProps) => {
    this.memBuffers = memBuffers
  }

  @action
  public setMemCached = (memCached: ServerStatusUsageProps) => {
    this.memCached = memCached
  }

  @action
  public setSwapUsage = (swapUsage: ServerStatusUsageProps) => {
    this.swapUsage = swapUsage
  }

  @action
  public setSwapCached = (swapCached: ServerStatusUsageProps) => {
    this.swapCached = swapCached
  }
}

export default new ServerStatus()
