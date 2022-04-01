import { conf } from '../Utils/components/conf'
class Main {
  public readonly id = 'ping'
  public readonly conf = conf?.[this.id]
  public readonly isEnable: boolean = Boolean(this.conf)
}
export const PingConstants = new Main()
