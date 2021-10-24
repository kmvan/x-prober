import { conf } from '../../Utils/src/components/conf'
class Main {
  public readonly id = 'serverBenchmark'
  public readonly conf = conf?.[this.id]
  public readonly isEnable: boolean = Boolean(this.conf)
}
export const ServerBenchmarkConstants = new Main()
