import { gettext } from '@/Language/src'
import { darken, lighten, rgba } from 'polished'
import { colorSchemeProps } from '../typings'
const light = '#ccc'
const dark = '#000'
const topDarkBottomLight = `linear-gradient(#000, #111)`
const colorSchemeDark: colorSchemeProps = {
  name: gettext('Dark'),
  isDark: true,
  color: dark,
  fg: dark,
  bg: light,
  'selection.fg': light,
  'selection.bg': rgba(dark, 0.95),
  'html.bg': dark,
  'body.fg': light,
  'body.bg': dark,
  'a.fg': light,
  'app.border': dark,
  'app.fg': light,
  'app.bg': '#222',
  'title.fg': light,
  'title.bg': dark,
  'sysLoad.fg': light,
  'sysLoad.bg': dark,
  'card.border': rgba('#000', 0.5),
  'card.fg': light,
  'card.bg': '#333',
  'card.hover.bg': `linear-gradient(to right, transparent, ${rgba(
    '#000',
    0.5
  )}, transparent)`,
  'card.legend.fg': light,
  'card.legend.bg': topDarkBottomLight,
  'progress.fg': light,
  'progress.bg': topDarkBottomLight,
  'progress.value.fg': light,
  'progress.value.bg': '#0c0',
  'progress.value.after.bg': `linear-gradient(${rgba('#fff', 0.5)}, ${rgba(
    '#fff',
    0.1
  )})`,
  'progress.value.before.bg': `linear-gradient(to right, ${rgba(
    '#fff',
    0.1
  )}, ${rgba('#fff', 0.3)}, ${rgba('#fff', 0.1)})`,
  'network.stats.upload': lighten(0.2, '#c24b00'),
  'network.stats.download': lighten(0.2, '#007400'),
  'network.node.fg': light,
  'network.node.bg': '#252525',
  'network.node.border': dark,
  'network.node.row.bg': `linear-gradient(to right, transparent, ${rgba(
    '#000',
    0.5
  )}, transparent)`,
  'ping.button.fg': light,
  'ping.button.bg': dark,
  'ping.result.fg': light,
  'ping.result.bg': dark,
  'status.success.fg': light,
  'status.success.bg': `linear-gradient(${darken(0.25, '#00e800')}, ${darken(
    0.2,
    '#00e800'
  )})`,
  'status.error.fg': light,
  'status.error.bg': `linear-gradient(${darken(0.45, '#b9b9b9')}, ${darken(
    0.4,
    '#b9b9b9'
  )})`,
  'search.fg': light,
  'search.bg': rgba(dark, 0.1),
  'search.hover.fg': light,
  'search.hover.bg': rgba(dark, 0.3),
  'benchmark.ruby.fg': dark,
  'benchmark.ruby.bg': rgba(dark, 0.1),
  'footer.fg': light,
  'footer.bg': dark,
  'nav.fg': light,
  'nav.bg': dark,
  'nav.hover.fg': dark,
  'nav.hover.bg': `linear-gradient(${light}, ${darken(0.15, light)})`,
  'nav.border': rgba(light, 0.1),
  'starMe.fg': darken(0.1, light),
  'starMe.bg': dark,
  'starMe.hover.fg': light,
  'starMe.hover.bg': dark,
  'toast.fg': light,
  'toast.bg': dark,
}
export default colorSchemeDark
