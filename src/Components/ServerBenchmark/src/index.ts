import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'

CardStore.addCard({
  id: 'serverBenchmark',
  title: gettext('Server Benchmark'),
  tinyTitle: gettext('Becnhmark'),
  priority: 800,
  component,
})
