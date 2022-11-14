import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { ColorSchemeStore } from '../stores'
import { colorSchemes } from '../stores/color-schemes'
import styles from './styles.module.scss'
export const ColorScheme: FC = observer(() => (
  <div className={styles.main}>
    {Object.entries(colorSchemes).map(([schemeId, { name, color }]) => (
      <a
        className={styles.link}
        data-active={schemeId === ColorSchemeStore.schemeId || undefined}
        title={name}
        key={schemeId}
        style={{ background: color }}
        onClick={() => ColorSchemeStore.setSchemeId(schemeId)}
      />
    ))}
  </div>
))
