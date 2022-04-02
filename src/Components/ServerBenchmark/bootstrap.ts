import { CardStore } from '../Card/stores'
import { gettext } from '../Language'
import { ServerBenchmark as component } from './components'
import { ServerBenchmarkConstants } from './constants'
export const ServerBenchmarkBoostrap = (): void => {
  const { id, isEnable } = ServerBenchmarkConstants
  isEnable &&
    CardStore.addCard({
      id,
      title: gettext('Server Benchmark'),
      tinyTitle: gettext('Becnhmark'),
      priority: 800,
      component,
    })
}
