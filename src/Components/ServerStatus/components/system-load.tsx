import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { gettext } from '../../Language'
import { template } from '../../Utils/components/template'
import { ServerStatusStore } from '../stores'
import styles from './styles.module.scss'
interface SysLoadGroupProps {
  sysLoad: number[]
  isCenter: boolean
}
export const SysLoadGroup: FC<SysLoadGroupProps> = ({ sysLoad, isCenter }) => {
  const minutes = [1, 5, 15]
  const loadHuman = sysLoad.map((load, i) => ({
    id: `${minutes[i]}minAvg`,
    load,
    text: template(gettext('{{minute}} minute average'), {
      minute: minutes[i],
    }),
  }))
  return (
    <div className={styles.loadGroup} data-center={isCenter || undefined}>
      {loadHuman.map(({ id, load, text }) => (
        <div className={styles.loadGroupItem} key={id} title={text}>
          {load.toFixed(2)}
        </div>
      ))}
    </div>
  )
}
interface SystemLoadProps {
  isCenter?: boolean
}
export const SystemLoad: FC<SystemLoadProps> = observer(
  ({ isCenter = false }) => (
    <CardGrid name={gettext('System load')}>
      <SysLoadGroup isCenter={isCenter} sysLoad={ServerStatusStore.sysLoad} />
    </CardGrid>
  ),
)
