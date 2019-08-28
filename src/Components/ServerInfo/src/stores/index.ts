import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'
import { configure, observable, action } from 'mobx'

configure({
  enforceActions: 'observed',
})

class ServerInfoStore {
  public ID = 'serverInfo'
  public conf = get(conf, this.ID)

  @observable public serverTime: string = get(this.conf, 'serverTime')
  @observable public serverUptime: string = get(this.conf, 'serverUptime')

  @action
  public setServerTime = (serverTime: string) => {
    this.serverTime = serverTime
  }

  @action
  public setServerUptime = (serverUptime: string) => {
    this.serverUptime = serverUptime
  }
}

export default new ServerInfoStore()
