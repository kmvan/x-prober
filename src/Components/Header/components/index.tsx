import type { FC } from 'react';
import styles from './index.module.scss';
import { HeaderName } from './name.tsx';
export const Header: FC = () => {
  return (
    <div className={styles.main}>
      {/* <HeaderBar /> */}
      <HeaderName />
    </div>
  );
};
