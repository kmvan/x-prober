// import { observer } from 'mobx-react-lite';
// import type { FC } from 'react';
// import { ColorSchemeStore } from '../stores';
// import { colorSchemes } from '../stores/color-schemes';
// import styles from './index.module.scss';
// export const ColorScheme: FC = observer(() => (
//   <div className={styles.main}>
//     {Object.entries(colorSchemes).map(([schemeId, { name, color }]) => (
//       <a
//         className={styles.link}
//         data-active={schemeId === ColorSchemeStore.schemeId || undefined}
//         key={schemeId}
//         onClick={() => ColorSchemeStore.setSchemeId(schemeId)}
//         style={{ background: color }}
//         title={name}
//       />
//     ))}
//   </div>
// ));
