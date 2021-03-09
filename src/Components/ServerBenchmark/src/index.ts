import { CardStore } from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import { ServerBenchmark as component } from './components'
import { ServerBenchmarkStore } from './stores'
CardStore.addCard({
  id: ServerBenchmarkStore.ID,
  title: gettext('Server Benchmark'),
  tinyTitle: gettext('Becnhmark'),
  priority: 800,
  component,
})
