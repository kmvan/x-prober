import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'
import { observable, configure, action } from 'mobx'

configure({
  enforceActions: 'observed',
})

export interface IServerStatusUsage {
  max: number
  value: number
}

class ServerStatus {
  public ID = 'serverStatus'
  public conf = get(conf, this.ID)

  @observable public sysLoad: number[] = this.conf.sysLoad
  @observable public cpuUsage: number = 10
  @observable public memRealUsage: IServerStatusUsage = this.conf.memRealUsage
  @observable public memBuffers: IServerStatusUsage = this.conf.memBuffers
  @observable public memCached: IServerStatusUsage = this.conf.memCached
  @observable public swapUsage: IServerStatusUsage = this.conf.swapUsage
  @observable public swapCached: IServerStatusUsage = this.conf.swapCached

  @action
  public setSysLoad = (sysLoad: number[]) => {
    this.sysLoad = sysLoad
  }

  @action
  public setCpuUsage = (cpuUsage: number) => {
    this.cpuUsage = cpuUsage
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
