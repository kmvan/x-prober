import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'
import { observable, configure, action, computed } from 'mobx'
import FetchStore from '~components/Fetch/src/stores'

configure({
  enforceActions: 'observed',
})

export interface IServerStatusUsage {
  max: number
  value: number
}

export interface IServerStatusCpuUsage {
  idle: number
  nice: number
  sys: number
  user: number
}

class ServerStatus {
  public readonly ID = 'serverStatus'
  public readonly conf = get(conf, this.ID)

  @observable public memRealUsage: IServerStatusUsage = this.conf.memRealUsage
  @observable public memBuffers: IServerStatusUsage = this.conf.memBuffers
  @observable public memCached: IServerStatusUsage = this.conf.memCached
  @observable public swapUsage: IServerStatusUsage = this.conf.swapUsage
  @observable public swapCached: IServerStatusUsage = this.conf.swapCached

  @computed
  get sysLoad(): number[] {
    return FetchStore.isLoading
      ? get(this.conf, 'sysLoad')
      : get(FetchStore.data, `${this.ID}.sysLoad`) || [0, 0, 0]
  }

  @computed
  get cpuUsage(): IServerStatusCpuUsage {
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
  public setMemRealUsage = (memRealUsage: IServerStatusUsage) => {
    this.memRealUsage = memRealUsage
  }

  @action
  public setMemBuffers = (memBuffers: IServerStatusUsage) => {
    this.memBuffers = memBuffers
  }

  @action
  public setMemCached = (memCached: IServerStatusUsage) => {
    this.memCached = memCached
  }

  @action
  public setSwapUsage = (swapUsage: IServerStatusUsage) => {
    this.swapUsage = swapUsage
  }

  @action
  public setSwapCached = (swapCached: IServerStatusUsage) => {
    this.swapCached = swapCached
  }
}

export default new ServerStatus()
